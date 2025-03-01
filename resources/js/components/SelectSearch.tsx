import { useState } from "react";
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
import { Button } from "@/components/ui/button";
import { cn } from "@/lib/utils";
import { Check, ChevronsUpDown, Loader2 } from "lucide-react";
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from "@/components/ui/command";
import { FormControl, FormItem, FormLabel, FormMessage } from "@/components/ui/form";
import { useQuery } from "@tanstack/react-query";
import { useDebounce } from "use-debounce";

interface SelectorProps<T> {
    label: string;
    value: string;
    searchKey: string;
    onChange: (value: string) => void;
    fetchItemsFn: (searchTerm: string) => Promise<T[]>
    renderDropdownList: (item: T) => string;
    renderDisplayOnFound: (items: T[]) => string;
}

export default function Selector<T extends { id: string }>({
    label,
    value,
    searchKey,
    onChange,
    fetchItemsFn,
    renderDropdownList,
    renderDisplayOnFound,
}: SelectorProps<T>) {
    const [searchTerm, setSearchTerm] = useState("");
    const [debouncedSearchTerm] = useDebounce(searchTerm, 500);

    const { data: items = [], isLoading } = useQuery<T[]>({
        queryKey: [searchKey, debouncedSearchTerm],
        queryFn: async () => {
            if (!debouncedSearchTerm) return [];
            return await fetchItemsFn(debouncedSearchTerm);
        },
    })

    const [openPopover, setOpenPopover] = useState(false);

    const handleCommandEmptyOrLoading = () => {
        if(isLoading) {
            return (
                <div className="flex justify-center items-center">
                    <Loader2 className="animate-spin" />
                </div>
            );
        }

        return <>Tidak Ditemukan</>
    }

    return (
        <FormItem>
            <FormLabel>{label}</FormLabel>
            <Popover open={openPopover} onOpenChange={setOpenPopover}>
                <PopoverTrigger asChild>
                    <FormControl>
                        <Button
                            variant="outline"
                            role="combobox"
                            className={cn("w-full justify-between", !value && "text-muted-foreground")}
                            aria-expanded={openPopover}
                        >
                            {renderDisplayOnFound(items)}
                            <ChevronsUpDown className="ml-2 h-4 w-4 shrink-0 opacity-50" />
                        </Button>
                    </FormControl>
                </PopoverTrigger>
                <PopoverContent className="w-[--radix-popover-trigger-width] p-0">
                    <Command shouldFilter={false}>
                        <CommandInput
                            className="w-full"
                            placeholder="Cari..."
                            onValueChange={(value) => setSearchTerm(value)}
                        />
                        <CommandList>
                            <CommandEmpty>{handleCommandEmptyOrLoading()}</CommandEmpty>
                            <CommandGroup>
                                {items.map((item) => (
                                    <CommandItem
                                        value={item.id}
                                        key={item.id}
                                        onSelect={(value) => {
                                            onChange(value);
                                            setOpenPopover(false);
                                        }}
                                    >
                                        <Check className={cn("mr-2 h-4 w-4", item.id === value ? "opacity-100" : "opacity-0")} />
                                        {renderDropdownList(item)}
                                    </CommandItem>
                                ))}
                            </CommandGroup>
                        </CommandList>
                    </Command>
                </PopoverContent>
            </Popover>
            <FormMessage />
        </FormItem>
    );
}
