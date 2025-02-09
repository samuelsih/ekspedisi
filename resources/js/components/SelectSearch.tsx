import { useState, useEffect } from "react";
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
import { Button } from "@/components/ui/button";
import { cn } from "@/lib/utils";
import { Check, ChevronsUpDown } from "lucide-react";
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from "@/components/ui/command";
import { FormControl, FormItem, FormLabel, FormMessage } from "@/components/ui/form";

interface SelectorProps<T> {
    label: string;
    value: string;
    onChange: (value: string) => void;
    fetchItemsFn: (searchTerm: string) => Promise<T[]>
    searchKey: keyof T;
    renderItem: (item: T) => string;
}

export default function Selector<T extends { id: string }>({
    label,
    value,
    onChange,
    fetchItemsFn,
    searchKey,
    renderItem
}: SelectorProps<T>) {
    const [searchTerm, setSearchTerm] = useState("");
    const [openPopover, setOpenPopover] = useState(false);
    const [items, setItems] = useState<T[]>([]);

    useEffect(() => {
        const loadItems = async () => {
            const result = await fetchItemsFn(searchTerm)
            setItems(result);
        };

        loadItems();
    }, [searchTerm]);

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
                            {value ? String(items.find((item) => item.id === value)?.[searchKey]) : "Cari..."}
                            <ChevronsUpDown className="ml-2 h-4 w-4 shrink-0 opacity-50" />
                        </Button>
                    </FormControl>
                </PopoverTrigger>
                <PopoverContent className="w-[--radix-popover-trigger-width] p-0">
                    <Command>
                        <CommandInput
                            className="w-full"
                            placeholder="Cari..."
                            onValueChange={(value) => setSearchTerm(value)}
                        />
                        <CommandList>
                            <CommandEmpty>Tidak Ditemukan</CommandEmpty>
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
                                        {renderItem(item)}
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
